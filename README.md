1. Install docker
1. Clone project. Copy the `.env.dist` file to a `.env` file. The username and password should be the credentials from https://devdocs.magento.com/guides/v2.4/install-gde/prereq/connect-auth.html
1. Run `bash cicd.sh`

If you want to run it again, first run `DOCKER_HOST=tcp://localhost:12375 docker stop magento fpm`. Unless you want to also stop the `dind` container, you will see an error like `... for 0.0.0.0:12375 failed: port is already allocated.`. This is ok and you can ignore it. It would go away if you stopped the `dind` container as well but then it would take a long time to run again.

If you want to clear your magento cicd environment within the `dind` container without losing all the cached magento cloud images you can run `DOCKER_HOST=tcp://localhost:12375 docker volume rm mage`. After you have stopped the containers as described above.   