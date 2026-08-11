<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Source+Sans+3:wght@400;500;600;700&display=swap');

.certificate {
  width: 1122px;
  height: 793px;
  box-sizing: border-box;
  padding: 18px;
  background: #f3eee6;
  color: #2c2825;
  box-shadow: 0 18px 40px -24px rgba(44,40,37,0.35);
}
/* Outer maroon frame — even gap via padding, then gold inner border */
.certificate-frame {
  box-sizing: border-box;
  height: 100%;
  border: 2px solid #7a1f2b;
  padding: 10px;
  background: #fffefb;
}
.certificate-inner {
  position: relative;
  box-sizing: border-box;
  height: 100%;
  border: 1.5px solid #c9a961;
  background:
    radial-gradient(ellipse 70% 50% at 50% 0%, rgba(201,169,97,0.14), transparent 58%),
    linear-gradient(180deg, #fffefb 0%, #faf6ef 100%);
}
.certificate-content {
  position: relative;
  z-index: 1;
  box-sizing: border-box;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 40px 64px 36px;
}
.certificate-brand {
  margin: 0;
  font-family: 'Source Sans 3', system-ui, sans-serif;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: #7a1f2b;
}
.certificate-brand span {
  color: #6b645e;
  font-weight: 500;
}
.certificate-divider {
  width: 140px;
  height: 1px;
  margin: 14px auto 0;
  background: linear-gradient(90deg, transparent, #c9a961, transparent);
  position: relative;
}
.certificate-divider::before {
  content: '✦';
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  background: #fffefb;
  padding: 0 0.4rem;
  color: #c9a961;
  font-size: 0.7rem;
}
.certificate-eyebrow {
  margin: 18px 0 0;
  font-family: 'Source Sans 3', system-ui, sans-serif;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.26em;
  text-transform: uppercase;
  color: #c9a961;
}
.certificate-heading {
  margin: 8px 0 0;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 2.75rem;
  font-weight: 700;
  line-height: 1.12;
  color: #7a1f2b;
}
.certificate-presented {
  margin: 22px 0 0;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 1.15rem;
  font-style: italic;
  color: #6b645e;
}
.certificate-recipient {
  margin: 8px 0 0;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 2.55rem;
  font-weight: 700;
  line-height: 1.15;
  color: #2c2825;
  border-bottom: 1.5px solid rgba(201,169,97,0.65);
  display: inline-block;
  min-width: 52%;
  max-width: 85%;
  padding: 0 1.1rem 0.4rem;
}
.certificate-body {
  margin: 18px auto 0;
  max-width: 40rem;
  font-family: 'Source Sans 3', system-ui, sans-serif;
  font-size: 1.02rem;
  line-height: 1.7;
  color: #5c564f;
}
.certificate-body strong {
  color: #2c2825;
  font-weight: 700;
}
.certificate-verse {
  margin: 14px auto 0;
  max-width: 34rem;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 1.02rem;
  font-style: italic;
  line-height: 1.55;
  color: #7a1f2b;
}
.certificate-meta {
  margin-top: 34px;
  width: 100%;
  max-width: 46rem;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  gap: 1.75rem;
  align-items: end;
}
.certificate-meta-value {
  margin: 0;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 1.12rem;
  font-weight: 600;
  border-bottom: 1px solid #d4cbbd;
  padding-bottom: 0.4rem;
}
.certificate-meta-label {
  margin: 0.45rem 0 0;
  font-family: 'Source Sans 3', system-ui, sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: #6b645e;
}
.certificate-seal {
  width: 86px;
  height: 86px;
  border-radius: 999px;
  border: 3px double #c9a961;
  display: grid;
  place-items: center;
  color: #7a1f2b;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 1.2rem;
  font-weight: 700;
  background:
    radial-gradient(circle at 35% 30%, rgba(255,255,255,0.65), transparent 45%),
    rgba(201,169,97,0.16);
  box-shadow: inset 0 0 0 4px rgba(122,31,43,0.06);
}
.certificate-ribbon {
  display: inline-block;
  margin-top: 16px;
  padding: 0.5rem 1.35rem;
  border-radius: 999px;
  background: linear-gradient(135deg, #7a1f2b, #9a2f3d);
  color: #fff;
  font-family: 'Source Sans 3', system-ui, sans-serif;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  box-shadow: 0 8px 18px -12px rgba(122,31,43,0.7);
}
@media print {
  .certificate {
    width: 297mm !important;
    height: 210mm !important;
    box-shadow: none !important;
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
  .certificate-frame,
  .certificate-inner {
    height: 100% !important;
  }
}
</style>
