import { useState, useEffect } from "react";

/**
 * Strip HTML tags from a string to prevent CSS-exfiltration and markup injection
 * via the custom CSS field.
 *
 * @param {string} input
 * @returns {string}
 */
function stripTags(input) {
  if (typeof input !== "string") return "";
  return input.replace(/<[^>]*>/g, "");
}

export default function Style({ attributes }) {
  const { clientId, style, additional, align } = attributes;
  const [css, setCSS] = useState();

  useEffect(() => {
    let css = `#${clientId} {width: ${style.width}; height: ${style.height}}}`;
    css += `#${clientId} {${align === "right" ? "margin-left:auto" : align === "center" ? "margin: auto" : ""}}`;
    css += `#${clientId} model-viewer{background-color: ${style.bgColor}}`;
    css += stripTags(additional?.CSS);
    setCSS(css);
  }, [style, clientId, additional, align]);

  return <style>{css}</style>;
}
