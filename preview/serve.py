#!/usr/bin/env python3
"""Serve the static reference preview only; this is not a Bagisto server."""

from argparse import ArgumentParser
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from urllib.parse import unquote, urlsplit

ROOT = Path(__file__).resolve().parent.parent
PREVIEW = (ROOT / 'preview').resolve()
ASSETS = (ROOT / 'public' / 'themes' / 'shop' / 'aponbasket').resolve()


class PreviewHandler(SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=str(ROOT), **kwargs)

    def do_GET(self):
        path = unquote(urlsplit(self.path).path)
        if path == '/':
            path = '/preview/index.html'
        candidate = (ROOT / path.lstrip('/')).resolve()
        allowed = (candidate.is_relative_to(PREVIEW) and candidate.suffix in {'.html', '.jpg'}) or candidate.is_relative_to(ASSETS)
        if not allowed or not candidate.is_file():
            self.send_error(404)
            return
        self.path = '/' + candidate.relative_to(ROOT).as_posix()
        super().do_GET()

    def do_HEAD(self):
        # Keep HEAD subject to the same allowlist as GET.
        self.send_error(405)


if __name__ == '__main__':
    parser = ArgumentParser(description=__doc__)
    parser.add_argument('--port', type=int, default=4173)
    args = parser.parse_args()
    print(f'Static AponBasket design reference on port {args.port} (not Bagisto)', flush=True)
    ThreadingHTTPServer(('0.0.0.0', args.port), PreviewHandler).serve_forever()
