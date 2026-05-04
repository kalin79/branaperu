export function stripHtml(html) {
    if (typeof html !== "string" || !html.trim()) return "";

    const div = document.createElement("div");
    div.innerHTML = html;

    return div.textContent
        .replace(/\s+/g, " ") // reemplaza múltiples espacios por uno solo
        .trim();
}

export function removeBreaks(html) {
    if (typeof html !== "string" || !html.trim()) return "";

    return html
        .replace(/<br\s*\/?>/gi, " ")
        .replace(/\s+/g, " ")
        .trim();
}
