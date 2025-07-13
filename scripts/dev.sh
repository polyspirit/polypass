#!/bin/bash
unset npm_config_prefix
export NVM_DIR="$HOME/.nvm"
source "$NVM_DIR/nvm.sh"
nvm use
vite --host www.gbo.com