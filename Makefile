phpstan:
	php -d memory_limit=512M vendor/bin/phpstan analyse --configuration=phpstan.neon.dist

release:
	@if [ -z "$(VERSION)" ]; then \
		echo "Error: VERSION is not set. Usage: make release VERSION=0.5.5"; \
		exit 1; \
	fi
	@echo "Releasing version $(VERSION)..."
	@git commit --allow-empty -m "chore: release v$(VERSION)"
	@git tag v$(VERSION)
	@git push origin v$(VERSION)
	@echo "Release v$(VERSION) pushed successfully."
