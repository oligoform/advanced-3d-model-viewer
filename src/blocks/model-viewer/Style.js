import { useState, useEffect } from "react";

/**
 * Sanitize custom CSS to prevent injection attacks.
 * Removes HTML tags, @import, @font-face, and dangerous CSS functions.
 */
const sanitizeCSS = (css) => {
  if (!css || typeof css !== 'string') {
    return '';
  }

  return css
    // Remove HTML tags
    .replace(/<[^>]*>/g, '')
    // Remove @import, @font-face, @media, @keyframes, @supports
    .replace(/@\s*(import|font-face|media|keyframes|supports|charset|namespace|document|page)[^;{]*[;{]/gi, '')
    // Remove expression(), url() with javascript:, and binding()
    .replace(/[\s\w-]*\s*:\s*(expression|binding)\s*\(/gi, '')
    .replace(/url\s*\(\s*['"]*\s*javascript\s*:/gi, '')
    // Remove behavior: (IE-specific, can load external content)
    .replace(/behavior\s*:\s*[^;}]*/gi, '');
};

export default function Style({ attributes }) {
  const { clientId, style, additional, align } = attributes;
  const [css, setCSS] = useState();

  useEffect(() => {
    let css = `#${clientId} {width: ${style.width}; height: ${style.height}}`;
    css += `#${clientId} {${align === "right" ? "margin-left:auto" : align === "center" ? "margin: auto" : ""}}`;
    css += `#${clientId} model-viewer{background-color: ${style.bgColor}}`;
    css += sanitizeCSS(additional?.CSS);
    setCSS(css);
  }, [style, clientId, additional, align]);

  return <style>{css}</style>;
}
