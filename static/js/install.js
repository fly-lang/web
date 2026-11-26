// Install page: downloads of any published release and the build instructions
// of the chosen version. The version list is rendered by the template (so it
// works without the API); the assets come from the GitHub releases API.
// ?version=<x> in the URL selects a version.

const API = window.FLY_RELEASES_API;
const REPO = window.FLY_REPO;
const LATEST = window.FLY_LATEST;

const PLATFORMS = [
  { key: 'windows', label: 'Windows', match: name => /win/i.test(name) },
  { key: 'macos',   label: 'macOS',   match: name => /macos|darwin/i.test(name) },
  { key: 'linux',   label: 'Linux',   match: name => /linux/i.test(name) },
];

const $ = id => document.getElementById(id);

function detectOS() {
  const ua = navigator.userAgent.toLowerCase();
  if (ua.includes('win')) return 'windows';
  if (ua.includes('mac')) return 'macos';
  return 'linux';
}

// fly-0.15.2-linux-x86_64.tar.gz → x86_64; fly-0.15.2-win-x64.zip → x64
function archOf(name) {
  const m = name.match(/(x86_64|x64|amd64|arm64|aarch64)/i);
  return m ? m[1] : '—';
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c =>
    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

const tag = v => 'v' + v;

let releases = null;   // version → release, from the API (null until loaded / on failure)
let apiFailed = false;

// The version menus (<details class="vmenu">, see docs-components.html): each
// item is a link with data-version; the menu's current version is data-current.
function hasVersion(menu, v) {
  return Array.from(menu.querySelectorAll('[data-version]')).some(a => a.dataset.version === v);
}

function setMenu(menu, v) {
  menu.dataset.current = v;
  menu.querySelector('.vmenu-current').textContent = v;
  const badge = menu.querySelector('summary .vmenu-badge');
  if (badge) badge.hidden = v !== LATEST;
  menu.querySelectorAll('[data-version]').forEach(a => {
    const on = a.dataset.version === v;
    a.classList.toggle('is-current', on);
    if (on) a.setAttribute('aria-current', 'true'); else a.removeAttribute('aria-current');
  });
}

function onPick(menu, handler) {
  menu.addEventListener('click', e => {
    const item = e.target.closest('[data-version]');
    if (!item) return;
    e.preventDefault();
    menu.open = false;
    menu.querySelector('summary').focus();
    handler(item.dataset.version);
  });
}

function renderDownloads(version) {
  const btns = $('js-detect-btns');
  const tbody = $('js-tbody');
  const releaseUrl = `${REPO}/releases/tag/${tag(version)}`;

  $('js-version').textContent = `Fly ${version}` + (version === LATEST ? ' (latest release)' : '');
  $('js-release-notes').href = releaseUrl;
  $('js-sums').href = `${REPO}/releases/download/${tag(version)}/SHA256SUMS`;

  const docs = $('js-docs');
  const docsUrl = docs.dataset.versions && docs.dataset.versions.split(' ').includes(version)
    ? `/docs/${version}/` : null;
  if (docsUrl) { docs.href = docsUrl; docs.hidden = false; } else { docs.hidden = true; }

  if (releases === null) {
    if (!apiFailed) return;   // still loading: the spinners stay
    btns.innerHTML = `<a href="${releaseUrl}" class="btn btn-primary">Download Fly ${escapeHtml(version)} on GitHub</a>`;
    tbody.innerHTML = `<tr><td colspan="4">Could not load the release assets — see
      <a href="${releaseUrl}">the release on GitHub</a>.</td></tr>`;
    return;
  }

  const release = releases.get(version);
  const assets = release ? release.assets.filter(a => !/SHA256SUMS/i.test(a.name)) : [];
  if (!release || assets.length === 0) {
    btns.innerHTML = `<p>No downloads published for ${escapeHtml(version)} — see
      <a href="${releaseUrl}">the release on GitHub</a>.</p>`;
    tbody.innerHTML = '<tr><td colspan="4">No packages for this version.</td></tr>';
    return;
  }

  const os = detectOS();
  const mine = assets.find(a => PLATFORMS.find(p => p.key === os).match(a.name));
  if (mine) {
    const label = PLATFORMS.find(p => p.key === os).label;
    btns.innerHTML = `
      <a href="${mine.browser_download_url}" class="btn btn-primary" download>
        Download Fly ${escapeHtml(version)} for ${label} (${escapeHtml(archOf(mine.name))})
      </a>`;
  } else {
    btns.innerHTML = '<p>No package for your platform in this version — see the table below.</p>';
  }

  const rows = [];
  for (const p of PLATFORMS) {
    const matching = assets.filter(a => p.match(a.name));
    if (matching.length === 0) {
      rows.push(`<tr><td>${p.label}</td><td>—</td><td>${escapeHtml(version)}</td><td>—</td></tr>`);
      continue;
    }
    for (const a of matching) {
      rows.push(`<tr>
        <td>${p.label}</td>
        <td>${escapeHtml(archOf(a.name))}</td>
        <td>${escapeHtml(version)}</td>
        <td><a href="${a.browser_download_url}" class="btn btn-ghost" download>Download</a></td>
      </tr>`);
    }
  }
  tbody.innerHTML = rows.join('');
}

function showBuild(version) {
  const blocks = document.querySelectorAll('.build-block');
  let shown = false;
  blocks.forEach(b => {
    const on = b.dataset.version === version;
    b.hidden = !on;
    shown = shown || on;
  });
  if (!shown) {
    blocks.forEach(b => { b.hidden = b.dataset.version !== LATEST; });
  }
  const menu = $('js-build');
  if (hasVersion(menu, version)) setMenu(menu, version);
}

function setUrl(version) {
  const url = new URL(window.location.href);
  if (version === LATEST) url.searchParams.delete('version');
  else url.searchParams.set('version', version);
  history.replaceState(null, '', url);
}

// Either menu switches the downloads and the build instructions together; a
// release newer than the site (no build page yet) only changes the downloads.
function selectRelease(version) {
  setMenu($('js-release'), version);
  renderDownloads(version);
  showBuild(version);
  setUrl(version);
}

function selectBuild(version) {
  showBuild(version);
  if (hasVersion($('js-release'), version)) {
    setMenu($('js-release'), version);
    renderDownloads(version);
  }
  setUrl(version);
}

async function loadReleases() {
  try {
    const res = await fetch(API + '?per_page=100');
    if (!res.ok) throw new Error(res.statusText);
    const list = await res.json();
    releases = new Map();
    const menu = $('js-release');
    const first = menu.querySelector('[data-version]');
    for (const r of list) {
      if (r.draft) continue;
      const v = r.tag_name.replace(/^v/, '');
      releases.set(v, r);
      // a release published after the site was built still shows up
      if (r.assets.length > 0 && first && !hasVersion(menu, v)) {
        const item = first.cloneNode(true);
        item.dataset.version = v;
        item.href = `/install/?version=${encodeURIComponent(v)}`;
        item.querySelector('.vmenu-item-v').textContent = v;
        item.querySelector('.vmenu-item-date').textContent = r.published_at
          ? new Date(r.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
          : '';
        item.querySelectorAll('.vmenu-badge').forEach(b => b.remove());
        first.parentNode.insertBefore(item, first);
      }
    }
  } catch {
    apiFailed = true;
  }
}

async function init() {
  const release = $('js-release');
  const build = $('js-build');
  onPick(release, selectRelease);
  onPick(build, selectBuild);

  const wanted = new URL(window.location.href).searchParams.get('version');
  const start = wanted && (hasVersion(release, wanted) || hasVersion(build, wanted)) ? wanted : LATEST;
  selectBuild(start);
  if (!hasVersion(release, start)) setMenu(release, LATEST);
  // the anchor (e.g. from /install/<version>/) points at a block that was hidden
  if (window.location.hash) {
    const target = document.getElementById(window.location.hash.slice(1));
    if (target) target.scrollIntoView();
  }

  await loadReleases();
  if (wanted && !hasVersion(build, wanted) && hasVersion(release, wanted)) {
    setMenu(release, wanted);   // a release newer than the site: downloads only
  }
  renderDownloads(release.dataset.current);
}

init();
