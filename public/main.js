const btn = document.getElementById('cartDropdown');
const panel = document.getElementById('cartPanel');
let loaded = false;

btn.addEventListener('click', async () => {
    if (!loaded) {
        const res = await fetch('cart.php');
        panel.innerHTML = await res.text();
        loaded = true;
    }
    panel.classList.toggle('show');
    btn.setAttribute('aria-expanded', panel.classList.contains('show'));
});

document.addEventListener('click', (e) => {
    if (!btn.contains(e.target) && !panel.contains(e.target)) {
        panel.classList.remove('show');
        btn.setAttribute('aria-expanded', 'false');
    }
});
