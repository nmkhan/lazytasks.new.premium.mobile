export function translate(key) {
    return window.appLocalizerPremium?.i18n?.[key] || key;
}
