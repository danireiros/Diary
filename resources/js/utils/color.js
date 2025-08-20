export function hexToRgb(hex){ const h=(hex||'').replace('#',''); if(h.length===3){ const r=parseInt(h[0]+h[0],16), g=parseInt(h[1]+h[1],16), b=parseInt(h[2]+h[2],16); return {r,g,b}; } if(h.length===6){ return { r:parseInt(h.slice(0,2),16), g:parseInt(h.slice(2,4),16), b:parseInt(h.slice(4,6),16) }; } return {r:128,g:128,b:128}; }
export function rgbToHex(r,g,b){ const c=(n)=>Math.max(0,Math.min(255,Math.round(n))).toString(16).padStart(2,'0'); return `#${c(r)}${c(g)}${c(b)}`; }
export function lightenHex(hex, amount=0.5){ const {r,g,b}=hexToRgb(hex||'#888'); const rr=r+(255-r)*amount, gg=g+(255-g)*amount, bb=b+(255-b)*amount; return rgbToHex(rr,gg,bb); }
export function getContrastText(hex){ const {r,g,b}=hexToRgb(hex||'#888'); const L=(c)=>{ c/=255; return c<=0.03928? c/12.92: Math.pow((c+0.055)/1.055, 2.4); }; const lum=0.2126*L(r)+0.7152*L(g)+0.0722*L(b); return lum>0.5? '#000' : '#fff'; }


