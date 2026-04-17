.PHONY: bootstrap up up-build down down-volumes

bootstrap:
	@echo "Copying .env.example files..."
	@find . -name ".env.example" | while read -r file; do \
		target="$${file%.example}"; \
		if [ ! -f "$$target" ]; then \
			cp "$$file" "$$target"; \
			echo "Created: $$target"; \
		else \
			echo "Skipped (already exists): $$target"; \
		fi \
	done
	@echo "Generating NEXTAUTH_SECRET..."
	@if [ -f ".env" ]; then \
		secret=$$(openssl rand -base64 32); \
		grep -q "NEXTAUTH_SECRET=" .env \
			&& sed -i "s|NEXTAUTH_SECRET=.*|NEXTAUTH_SECRET=$$secret|" .env \
			|| echo "NEXTAUTH_SECRET=$$secret" >> .env; \
		echo "Done."; \
	fi

up:
	docker compose up -d

up-build:
	docker compose up -d --build --force-recreate

down:
	docker compose down

down-volumes:
	docker compose down -v

api-test:
	docker compose exec api php artisan test