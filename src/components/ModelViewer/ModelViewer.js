import { useRef, useEffect, useState } from "react";

import "./style.scss";
import FullScreen from "../icons/FullScreen";
import Close from "../icons/Close";

export default function ModelViewer({ attributes, setViewer }) {
  const { clientId, model, attrs } = attributes;
  const [enableOptions, setEnableOptions] = useState("");

  const ref = useRef();

  useEffect(() => {
    if (ref.current) {
      const instance = ref.current.querySelector("model-viewer");
      // Use namespaced global to avoid pollution
      if (!window.a3dmv) {
        window.a3dmv = {};
      }
      window.a3dmv[clientId] = instance;
      setViewer(instance);
    }
  }, [attributes, clientId, setViewer]);

  useEffect(() => {
    const attributes = Object.fromEntries(Object.entries(attrs).map(([key, value]) => [key, value ? value : undefined]));

    if (["eager", "lazy"].includes(attrs.loading)) {
      delete attributes.reveal;
    }
    setEnableOptions(attributes);
  }, [attrs]);

  return (
    <>
      <div id={clientId} ref={ref} className="a3dmv-model-viewer">
        <model-viewer {...enableOptions} src={model.model_url} poster={model.poster_url}>
          <span className="fullscreen-icon">
            <FullScreen className="open" onClick={() => ref.current?.querySelector("model-viewer").requestFullscreen()} />
            <Close className="close" onClick={() => document.exitFullscreen()} />
          </span>
        </model-viewer>
      </div>
    </>
  );
}
