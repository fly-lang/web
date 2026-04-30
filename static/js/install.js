const API = window.FLY_RELEASES_API;

const PLATFORMS = [
  { key: 'windows', label: 'Windows 64-bit', match: asset => /windows.*x64|x64.*windows/i.test(asset.name) },
  { key: 'macos',   label: 'macOS',           match: asset => /macos|darwin/i.test(asset.name) },
  { key: 'linux',   label: 'Linux 64-bit',    match: asset => /linux.*x64|x64.*linux/i.test(asset.name) },
];

function detectOS() {
  const ua = navigator.userAgent.toLowerCase();
  if (ua.includes('win'))  return 'windows';
  if (ua.includes('mac'))  return 'macos';
  return 'linux';
}

async function load() {
  let release;
  try {
    const res = await fetch(API);
    if (!res.ok) throw new Error(res.statusText);
    const releases = await res.json();
    release = releases[0];
    if (!release) throw new Error('No releases found');
  } catch {
    document.getElementById('js-version').textContent = 'Could not load release info.';
    document.getElementById('js-detect-btns').innerHTML = '';
    document.getElementById('js-tbody').innerHTML =
      '<tr><td colspan="4">Failed to fetch releases from GitHub.</td></tr>';
    return;
  }

  const version = release.tag_name;
  document.getElementById('js-version').textContent = `Version ${version} (pre-release)`;

  // Map assets by platform
  const byPlatform = {};
  for (const p of PLATFORMS) {
    byPlatform[p.key] = release.assets.find(p.match) || null;
  }

  // Detect button
  const os = detectOS();
  const detected = byPlatform[os];
  const btnsEl = document.getElementById('js-detect-btns');
  if (detected) {
    btnsEl.innerHTML = `
      <a href="${detected.browser_download_url}" class="btn btn-primary" download>
        Download for ${PLATFORMS.find(p => p.key === os).label}
      </a>`;
  } else {
    btnsEl.innerHTML = '<p>No package found for your platform — see the table below.</p>';
  }

  // Full table
  const rows = PLATFORMS.map(p => {
    const a = byPlatform[p.key];
    const link = a
      ? `<a href="${a.browser_download_url}" class="btn btn-ghost" download>Download</a>`
      : '—';
    return `<tr>
      <td>${p.label}</td>
      <td>x64</td>
      <td>${version}</td>
      <td>${link}</td>
    </tr>`;
  }).join('');

  document.getElementById('js-tbody').innerHTML = rows;
}

load();
