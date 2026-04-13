import { useState, useEffect } from "react";

/**
 * Sanitize a CSS string to prevent HTML injection and style-tag break-out.
 * Removes all HTML tags and strips bare `<` characters that could be used
 * to inject `</style>` or `<script>` sequences.
 *
 * @param {string} input
 * @returns {string}
 */
function sanitizeCSS(input) {
  if (typeof input !== "string") return "";
  // First remove complete tags, then strip any remaining `<` characters.
  return input.replace(/<[^>]*>/g, "").replace(/</g, "");
}

/**
 * Validate a CSS dimension value (e.g. "100px", "50%", "20vh").
 * Returns the value if it matches an expected pattern, or a safe fallback otherwise.
 *
 * @param {string} value
 * @param {string} fallback
 * @returns {string}
 */
function sanitizeCSSValue(value, fallback = "") {
  if (typeof value !== "string") return fallback;
  return /^\d+(\.\d+)?(px|%|vh|vw|em|rem|pt|cm|mm|in)$/.test(value.trim())
    ? value.trim()
    : fallback;
}

/**
 * Validate a CSS color value (hex, named color, rgb/rgba/hsl/hsla, or transparent).
 * Returns the value if it looks safe, or 'transparent' otherwise.
 *
 * @param {string} value
 * @returns {string}
 */
function sanitizeCSSColor(value) {
  if (typeof value !== "string") return "transparent";
  const trimmed = value.trim();
  if (
    /^#[0-9a-fA-F]{3,8}$/.test(trimmed) ||
    /^[a-zA-Z]+$/.test(trimmed) ||
    /^(rgb|rgba|hsl|hsla)\([^)]*\)$/.test(trimmed)
  ) {
    return trimmed;
  }
  return "transparent";
}

export default function Style({ attributes }) {
  const { clientId, style, additional, align } = attributes;
  const [css, setCSS] = useState();

  useEffect(() => {
    const safeWidth  = sanitizeCSSValue(style.width, "100%");
    const safeHeight = sanitizeCSSValue(style.height, "400px");
    const safeColor  = sanitizeCSSColor(style.bgColor);

    let css = `#${clientId} {width: ${safeWidth}; height: ${safeHeight}}}`;
    css += `#${clientId} {${align === "right" ? "margin-left:auto" : align === "center" ? "margin: auto" : ""}}`;
    css += `#${clientId} model-viewer{background-color: ${safeColor}}`;
    css += sanitizeCSS(additional?.CSS);
    setCSS(css);
  }, [style, clientId, additional, align]);

  return <style>{css}</style>;
}
