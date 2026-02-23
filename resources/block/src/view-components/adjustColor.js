
export function adjustColor(hex) {
    if (!hex || typeof hex !== 'string') return hex;

    hex = hex.replace('#', '');

    // Expand shorthand (#rgb → rrggbb)
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }

    if (hex.length !== 6) return '#' + hex;

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    // Perceived luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    // Light → darken (factor < 1), Dark → lighten (factor > 1)
    const factor = luminance > 0.5 ? 0.9 : 1.1;

    const adjust = (channel) => {
        const adjusted = luminance > 0.5
            ? Math.round(channel * factor)
            : Math.round(channel + (255 - channel) * (factor - 1));
        return Math.min(255, Math.max(0, adjusted));
    };

    const toHex = (val) => val.toString(16).padStart(2, '0');

    return '#' + toHex(adjust(r)) + toHex(adjust(g)) + toHex(adjust(b));
}
