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

export default function Style({ attributes }) {
  const { clientId, style, additional, align } = attributes;
  const [css, setCSS] = useState();

  useEffect(() => {
    let css = `#${clientId} {width: ${style.width}; height: ${style.height}}}`;
    css += `#${clientId} {${align === "right" ? "margin-left:auto" : align === "center" ? "margin: auto" : ""}}`;
    css += `#${clientId} model-viewer{background-color: ${style.bgColor}}`;
    css += sanitizeCSS(additional?.CSS);
    setCSS(css);
  }, [style, clientId, additional, align]);

  return <style>{css}</style>;
}
