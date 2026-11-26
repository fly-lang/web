// Version menus (<details class="vmenu">, templates/docs-components.html).
// They open and work without JS; this adds what a native select would do:
// close on outside click and Escape, one open at a time, arrow-key navigation.
(() => {
  const menus = () => document.querySelectorAll('details.vmenu');
  const items = menu => Array.from(menu.querySelectorAll('.vmenu-item'));

  document.addEventListener('click', e => {
    menus().forEach(m => { if (m.open && !m.contains(e.target)) m.open = false; });
  });

  document.addEventListener('toggle', e => {
    const menu = e.target;
    if (!(menu instanceof HTMLDetailsElement) || !menu.classList.contains('vmenu') || !menu.open) return;
    menus().forEach(m => { if (m !== menu) m.open = false; });
  }, true);

  document.addEventListener('keydown', e => {
    const menu = e.target.closest && e.target.closest('details.vmenu');
    if (!menu) return;
    const list = items(menu);
    const at = list.indexOf(document.activeElement);

    if (e.key === 'Escape' && menu.open) {
      menu.open = false;
      menu.querySelector('summary').focus();
      e.preventDefault();
    } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      menu.open = true;
      if (at < 0) {
        // from the button: land on the current version
        (list.find(a => a.classList.contains('is-current')) || list[0]).focus();
      } else {
        const step = e.key === 'ArrowDown' ? 1 : -1;
        list[(at + step + list.length) % list.length].focus();
      }
      e.preventDefault();
    } else if ((e.key === 'Home' || e.key === 'End') && menu.open) {
      (e.key === 'Home' ? list[0] : list[list.length - 1]).focus();
      e.preventDefault();
    }
  });
})();
