#!/usr/bin/env python3
"""ArtisRaw QA helper — structural a11y/SEO audit + responsive screenshots via CDP.

Used at each phase's QA gate. Drives headless Chrome with real mobile emulation
(plain `chrome --screenshot` does NOT apply the meta-viewport, so it misreports
mobile layout — always use this).

Usage:
    python3 tools/qa.py <url> [out_prefix]
Requires: Google Chrome, `pip install websocket-client`.
"""
import base64, json, subprocess, sys, time, urllib.request
import websocket  # websocket-client

CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
PORT = 9222

AUDIT = r"""(()=>{
  const q=s=>Array.from(document.querySelectorAll(s));
  const headings=q('h1,h2,h3,h4,h5,h6').map(h=>+h.tagName[1]);
  let skips=0; for(let k=1;k<headings.length;k++){if(headings[k]-headings[k-1]>1)skips++;}
  return {
    h1count:q('h1').length,
    landmarks:{header:q('header').length,navLabeled:q('nav[aria-label]').length,main:q('main').length,footer:q('footer').length},
    skipLink:!!document.querySelector('a.skip-link'),
    htmlLang:document.documentElement.lang||null,
    imgsNoAlt:q('img').filter(im=>!im.hasAttribute('alt')).length,
    btnsNoName:q('button').filter(b=>!(b.textContent.trim()||b.getAttribute('aria-label')||b.querySelector('.visually-hidden'))).length,
    headingSkips:skips,
    canonical:q('link[rel=canonical]').length,
    metaDesc:q('meta[name=description]').length,
    scrollWidth:document.documentElement.scrollWidth,
    clientWidth:document.documentElement.clientWidth,
    jsonldTypes:q('script[type="application/ld+json"]').map(s=>{try{return JSON.parse(s.textContent)['@type']}catch(e){return 'PARSE_ERR'}}),
    title:document.title
  };
})()"""


def main():
    if len(sys.argv) < 2:
        print(__doc__); sys.exit(1)
    url = sys.argv[1]
    prefix = sys.argv[2] if len(sys.argv) > 2 else "/tmp/qa"

    proc = subprocess.Popen(
        [CHROME, "--headless=new", "--disable-gpu", f"--remote-debugging-port={PORT}",
         "--remote-allow-origins=*", "--user-data-dir=/tmp/cdp-prof", "about:blank"],
        stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    try:
        time.sleep(2)
        ws_url = json.load(urllib.request.urlopen(f"http://127.0.0.1:{PORT}/json"))[0]["webSocketDebuggerUrl"]
        ws = websocket.create_connection(ws_url, max_size=None)
        n = [0]
        def cmd(m, p=None):
            n[0] += 1
            ws.send(json.dumps({"id": n[0], "method": m, "params": p or {}}))
            while True:
                r = json.loads(ws.recv())
                if r.get("id") == n[0]:
                    return r.get("result")
        cmd("Page.enable")
        for label, metrics in (("desktop", {"width":1280,"height":860,"deviceScaleFactor":1,"mobile":False}),
                               ("mobile",  {"width":390,"height":844,"deviceScaleFactor":2,"mobile":True})):
            cmd("Emulation.setDeviceMetricsOverride", metrics)
            cmd("Page.navigate", {"url": url}); time.sleep(1.5)
            shot = f"{prefix}-{label}.png"
            open(shot, "wb").write(base64.b64decode(cmd("Page.captureScreenshot", {"format":"png"})["data"]))
            if label == "mobile":
                audit = cmd("Runtime.evaluate", {"expression": AUDIT, "returnByValue": True})["result"]["value"]
                print(json.dumps(audit, indent=2))
            print(f"saved {shot}")
        ws.close()
    finally:
        proc.terminate()


if __name__ == "__main__":
    main()
