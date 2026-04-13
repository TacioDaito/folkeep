const fs = require('fs');

const MAX_DIFF_CHARS = 80_000;
let diff = fs.readFileSync('pr.diff', 'utf8');

if (!diff.trim()) {
  console.log('Empty diff, skipping review.');
  process.exit(0);
}

if (diff.length > MAX_DIFF_CHARS) {
  diff = diff.slice(0, MAX_DIFF_CHARS) + '\n\n[diff truncated — too large to review in full]';
}

const prompt = `You are a senior Laravel/PHP engineer doing a code review.

PR Title: ${process.env.PR_TITLE}
PR Description: ${process.env.PR_BODY || 'No description provided.'}

Review the diff below and provide clear, actionable feedback. Focus on:
- Bugs and logic errors
- Security issues (SQL injection, auth bypasses, mass assignment, exposed secrets)
- Laravel best practices (Eloquent, service layer, Form Requests, policies)
- Performance concerns (N+1 queries, missing indexes, synchronous jobs that should be queued)
- Code clarity and maintainability

Structure your response exactly like this:

## Summary
One short paragraph describing what this PR does.

## Findings
Use these severity labels — omit any section that has no findings:

[Critical] — must fix before merging
[Warning]  — should fix, potential issues
[Note]     — optional improvements

For each finding, reference the relevant file and line from the diff when possible.

## Verdict
One of: Approved | Needs Changes | Needs Discussion

\`\`\`diff
${diff}
\`\`\``;

async function callGemini() {
  const model = 'gemini-2.5-pro';
  const url = `https://generativelanguage.googleapis.com/v1beta/models/${model}:generateContent?key=${process.env.GEMINI_API_KEY}`;

  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      contents: [
        { role: 'user', parts: [{ text: prompt }] }
      ],
      generationConfig: {
        maxOutputTokens: 2048,
        temperature: 0.2,
      },
    }),
  });

  if (!res.ok) throw new Error(`Gemini API error: ${res.status} ${await res.text()}`);
  const data = await res.json();
  return data.candidates[0].content.parts[0].text;
}

async function deleteOldReviews() {
  const [owner, repo] = process.env.REPO.split('/');
  const res = await fetch(
    `https://api.github.com/repos/${owner}/${repo}/issues/${process.env.PR_NUMBER}/comments?per_page=100`,
    { headers: { Authorization: `Bearer ${process.env.GITHUB_TOKEN}` } }
  );
  const comments = await res.json();
  const botComments = comments.filter(c =>
    c.user.login === 'github-actions[bot]' &&
    c.body.startsWith('## Gemini Code Review')
  );
  await Promise.all(botComments.map(c =>
    fetch(`https://api.github.com/repos/${owner}/${repo}/issues/comments/${c.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${process.env.GITHUB_TOKEN}` },
    })
  ));
}

async function postComment(body) {
  const [owner, repo] = process.env.REPO.split('/');
  const res = await fetch(
    `https://api.github.com/repos/${owner}/${repo}/issues/${process.env.PR_NUMBER}/comments`,
    {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${process.env.GITHUB_TOKEN}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ body }),
    }
  );
  if (!res.ok) throw new Error(`GitHub API error: ${res.status} ${await res.text()}`);
}

(async () => {
  try {
    console.log('Fetching review from Gemini...');
    const review = await callGemini();

    console.log('Cleaning up previous review comments...');
    await deleteOldReviews();

    const comment = [
      '## Gemini Code Review',
      '',
      review,
      '',
      '---',
      `Reviewed commit \`${process.env.COMMIT_SHA.slice(0, 7)}\` · [Workflow run](https://github.com/${process.env.REPO}/actions)`,
    ].join('\n');

    await postComment(comment);
    console.log('Review posted.');
  } catch (err) {
    console.error(err.message);
    process.exit(1);
  }
})();
