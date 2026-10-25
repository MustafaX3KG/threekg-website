const http = require("http"), fs = require("fs"), path = require("path");
const root = __dirname;
const types = {".html":"text/html",".css":"text/css",".js":"text/javascript",".mp4":"video/mp4",".jpg":"image/jpeg",".svg":"image/svg+xml",".txt":"text/plain",".json":"application/json"};
http.createServer((req, res) => {
  let p = decodeURIComponent(req.url.split("?")[0]);
  if (p === "/") p = "/index.html";
  const f = path.join(root, p);
  if (!f.startsWith(root) || !fs.existsSync(f) || fs.statSync(f).isDirectory()) { res.writeHead(404); res.end("nf"); return; }
  res.writeHead(200, { "Content-Type": types[path.extname(f)] || "application/octet-stream", "Cache-Control": "no-store" });
  fs.createReadStream(f).pipe(res);
}).listen(8123, () => console.log("serving on 8123"));
