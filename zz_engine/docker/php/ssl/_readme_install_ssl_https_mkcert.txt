https://github.com/FiloSottile/mkcert

Install mkcert:

sudo apt install libnss3-tools
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
sudo apt-get install build-essential
brew install mkcert
mkcert -install

Load certificates

mkcert -cert-file localhost.crt -key-file localhost.key  "*.localhost" localhost 127.0.0.1 ::1 classifieds.localhost www.classifieds.localhost
