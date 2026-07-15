/**
 * Custom entry point for cPanel-style Node.js hosting (Phusion Passenger).
 * Passenger starts this file directly with `node server.js` and proxies
 * requests to whatever port it tells us via the PORT env var — `next start`
 * alone doesn't work under Passenger, so we start Next's server manually.
 */
const { createServer } = require("http");
const next = require("next");

const port = process.env.PORT || 3000;
const app = next({ dev: false });
const handle = app.getRequestHandler();

app.prepare().then(() => {
  createServer((req, res) => {
    handle(req, res);
  }).listen(port, () => {
    console.log(`Ready on port ${port}`);
  });
});
