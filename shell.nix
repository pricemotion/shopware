let
  pkgs = import (fetchTarball https://github.com/NixOS/nixpkgs/archive/e76c78d20685a043d23f5f9e0ccd2203997f1fb1.tar.gz) {};
in
  pkgs.mkShell {
    packages = with pkgs; [esbuild];
  }
