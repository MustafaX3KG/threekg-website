#!/usr/bin/env node
"use strict";
const fs = require("fs"), path = require("path"), http = require("http"), cp = require("child_process");
const root = path.resolve(__dirname, "..");
const blocks = path.join(root, "wp-plugins", "threekg-core", "blocks");
for (const slug of fs.readdirSync(blocks)) {
  const file = path.join(blocks, slug, "block.json");
  const meta = JSON.parse(fs.readFileSync(file, "utf8"));
  if (meta.name !== `threekg/${slug}` || meta.apiVersion !== 3 || meta.acf?.blockVersion !== 3 || meta.acf?.renderCallback !== "threekg_core_render_block") throw new Error(`Invalid metadata: ${file}`);
}
const docs = ["front-page.html", "subdir-front-page.html", "plugin-active-saved.html", "plugin-active-saved-subdir.html"];
const server = cp.spawn(process.execPath, [path.join(__dirname, "render-server.js")], {stdio:["ignore","pipe","pipe"]});
const get = url => new Promise((resolve, reject) => http.get(url, r => { r.resume(); r.on("end", () => resolve(r.statusCode)); }).on("error", reject));
(async () => {
  await new Promise(r => setTimeout(r, 350));
  let checked = 0;
  for (const doc of docs) {
    const html = fs.readFileSync(path.join(__dirname, "render", doc), "utf8");
    const urls = [...html.matchAll(/(?:src|href)="([^"]+\.(?:css|js|jpg|png|svg|mp4)(?:\?[^"#]*)?)"/g)].map(m => m[1]).filter(u => u.startsWith("/"));
    for (const url of new Set(urls)) {
      const code = await get(`http://localhost:8124${url}`);
      if (code !== 200) throw new Error(`${doc}: ${url} returned ${code}`);
      checked++;
    }
  }
  const missing = await get("http://localhost:8124/wp-content/themes/threekg/assets/missing-phase3.file");
  if (missing !== 404) throw new Error("Harness server did not return a real 404.");
  console.log(`validated 8 block.json files and ${checked} root/subdirectory asset requests; negative path returned 404`);
})().finally(() => server.kill());

