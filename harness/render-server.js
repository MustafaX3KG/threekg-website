#!/usr/bin/env node
/*
 * Render harness server for the ThreeKG WP theme (evidence only, not shipped).
 * Maps the fake WordPress URLs used by the templates onto the real tree:
 *   /wp-content/themes/threekg/*  (or /(subdir)/wp-content/...)  -> wp-theme/threekg/*
 *   /build/render/*                                       -> build/render/*
 * Everything else 404s so missing assets are actually visible.
 */
const http = require("http"), fs = require("fs"), path = require("path");
const root = __dirname;
const themeDir = path.join(root, "..", "wp-theme", "threekg");
const catch404 = [];
const types = {".html":"text/html",".css":"text/css",".js":"text/javascript",".mp4":"video/mp4",".jpg":"image/jpeg",".svg":"image/svg+xml",".png":"image/png",".json":"application/json",".md":"text/plain"};
const seen = new Set();

http.createServer((req, res) => {
  const p = decodeURIComponent(req.url.split("?")[0]).replace(/\/+$/, "") || "/";
  const report404 = () => {
    catch404.push(p);
    res.writeHead(404, {"Content-Type":"text/plain"});
    res.end("404 " + p);
  };

  // normalize: allow optional subdir prefix
  const m = p.match(/^\/([^/]+)\/wp-content\/themes\/threekg\/(.*)$/);
  let themeFile = null;
  if (m) themeFile = path.join(themeDir, m[2]);
  else if (/^\/wp-content\/themes\/threekg\//.test(p)) themeFile = path.join(themeDir, p.replace(/^\/wp-content\/themes\/threekg\//, ""));

  if (themeFile) {
    if (!themeFile.startsWith(themeDir) || !fs.existsSync(themeFile) || fs.statSync(themeFile).isDirectory()) return report404();
    res.writeHead(200, {"Content-Type": types[path.extname(themeFile)] || "application/octet-stream", "Cache-Control":"no-store"});
    return fs.createReadStream(themeFile).pipe(res);
  }

  if (/^\/build\/render\//.test(p) || p === "/build/render") {
    let rel = p === "/build/render" ? "index.html" : p.replace(/^\/build\/render\//, "");
    if (!/\./.test(rel)) rel += "/index.html";
    const f = path.join(root, "render", rel);
    if (!f.startsWith(path.join(root, "render")) || !fs.existsSync(f) || fs.statSync(f).isDirectory()) return report404();
    res.writeHead(200, {"Content-Type": types[path.extname(f)] || "text/html", "Cache-Control":"no-store"});
    return fs.createReadStream(f).pipe(res);
  }

  report404();
}).listen(8124, () => console.log("render harness on 8124"));
