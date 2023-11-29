function isColorLight(hexColor) {
    // Convert hex to RGB
    const r = parseInt(hexColor.slice(1, 3), 16);
    const g = parseInt(hexColor.slice(3, 5), 16);
    const b = parseInt(hexColor.slice(5, 7), 16);

    // Calculate luminance (brightness)
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    // Decide if the color is light based on luminance
    return luminance > 0.5;
}