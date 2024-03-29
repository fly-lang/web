<p align="center">
  <a href="https://github.com/fly-lang/web">
    <img src="https://github.com/fly-lang/graphics/blob/main/logo/fly_logo_300.png?raw=true" alt="Logo" width="300" height="300">
  </a>

  <h3 align="center">Fly - Web</h3>

  <p align="center">
    Website of the Fly Project
    <br />
    <br />
    <a href="https://flylang.org">View Website</a>
    ·
    <a href="https://github.com/fly-lang/web/issues">Report Bug / Request Feature</a>
    ·
    <a href="https://github.com/fly-lang/web/discussions">Open a Discussion</a>
  </p>
</p>

## About the Project
This repository contains the source code of the website for the Fly Project, it contains web pages, images and all files need to working.

### Build with
This website uses the following Frameworks:
- [JQuery](https://jquery.com/)
- [Bootstrap](https://getbootstrap.com/)
- [Fork Awesome](https://forkaweso.me/)

## Getting Started
Here you can find instruction how to deploy the web application in your environment by Docker.
Build the Docker image:
`docker build . -t "fly-web:latest"`

Now you can start your Docker image at http://localhost:8080
`docker run -p 8080:80 fly-web:latest`

If you prefer working on the code development and see changes in realtime you can bind the source directory:
```
docker run -d \
-p 8080:80 \
-it \
--name devweb \
--mount type=bind,source="$(pwd)"/public,target=/app \
--mount type=bind,source="$(pwd)"/private,target=/private \
fly-web:latest
```

For stopping
`docker container stop devweb`

### Prerequisites
In order to see the webpages of this site you need:
 - PHP (min version 5.X)
 - Webserver with PHP module support like: Apache or Nginx

## Usage
You can contribute to this project for improve indirectly the Fly Programming Language main project.
You can deploy this website in your environment for fixes and enhancements, or only for testing.
For more info about this project:
 - For how to contribute see [CONTRIBUTING.md](CONTRIBUTING.md)
 - For know Authors see [AUTHORS.md](AUTHORS.md)
 - See [LICENSE](LICENSE)
 
 ## Contact
Twitter: [@fly_lang](https://twitter.com/fly_lang)
 
Email: [dev@flylang.org](mailto:dev@flylang.org)

Website: [flylang.org](https://flylang.org)
 
