REGISTRY := ci.marekurban.de/download-page
TAG := latest
PROJECT := download-page
COMPOSEFILE := dev/docker-compose.yml
USER_SQL := dev/user.sql

# Local environment variables should be loaded
# from local .env file only on developer computers.
# On GitLab .env should not be loaded
ifndef $(CI_ENVIRONMENT_NAME)
include .env
export
endif

.PHONY: help
help:
	@echo -e ""
	@echo -e "Variables:"
	@echo -e "TAG      - Docker image tag (default: latest)"
	@echo -e "REGISTRY - Docker registry (default: ci.marekurban.de/download-page)"
	@echo -e "PROJECT  - Project name (default: download-page)"
	@echo -e "USER_SQL - SQL script with user data (dev/user.sql)"
	@echo -e ""
	@echo -e "Targets:"
	@echo -e "build    - Build the Docker image"
	@echo -e "run      - Build and start the local development environment"
	@echo -e "stop     - Stop the local develeopment environment"
	@echo -e "test     - Make a simple availability test"
	@echo -e "logs     - Show logs"
	@echo -e "push     - Push docker image to registry"
	@echo -e "clean    - Remove docker image from local storage"
	@echo -e ""

.PHONY: build
build:
	docker build --pull --no-cache --label "org.label-schema.version=$(TAG)" -t $(REGISTRY)/$(PROJECT):$(TAG) -f build/Dockerfile .

.PHONY: run
run: _extract-data _run _set-user

.PHONY: push
push:
	docker push $(REGISTRY)/$(PROJECT):$(TAG)

.PHONY: clean
clean:
	docker rmi $(REGISTRY)/$(PROJECT):$(TAG)

.PHONY: test
test: _run _set-user _test _stop

.PHONY: logs
logs:
	docker-compose -f $(COMPOSEFILE) logs -f

.PHONY: stop
stop: _stop

### internal functions
.PHONY: _extract-data
_extract-data:
	tar -xf dev/download.tar.xz -C app/

.PHONY: _test
_test:
	sleep 5
	bats test/test.bats

.PHONY: _run
_run:
	docker-compose -f $(COMPOSEFILE) up -d --build --force-recreate

.PHONY: _stop
_stop:
	docker-compose -f $(COMPOSEFILE) rm --stop --force -v

.PHONY: _set-user
_set-user:
	docker exec -i $(DB_DP_HOSTNAME) mysql -uroot -p$(DB_DP_PASSWORD) --force \
		-h127.0.0.1 < $(USER_SQL)
