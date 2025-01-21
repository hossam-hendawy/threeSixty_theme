
/**
 * @param {SVGElement} element - Element to get the bounding box for
 * @param {boolean} [withoutTransforms=false] - If true, transforms will not be calculated
 * @param {SVGElement} [toElement] - Element to calculate bounding box relative to
 * @returns {{cx: number, cy: number, x: number, width: number, y: number, height: number}} Coordinates and dimensions of the real bounding box
 */
export function getBBox(element, withoutTransforms, toElement) {

  const svg = element.ownerSVGElement;

  if (!svg) {
    return { x: 0, y: 0, cx: 0, cy: 0, width: 0, height: 0 };
  }

  const r = element.getBBox();

  if (withoutTransforms) {
    return {
      x: r.x,
      y: r.y,
      width: r.width,
      height: r.height,
      cx: r.x + r.width / 2,
      cy: r.y + r.height / 2
    };
  }

  const p = svg.createSVGPoint();

  const matrix = (toElement || svg).getScreenCTM().inverse().multiply(element.getScreenCTM());

  p.x = r.x;
  p.y = r.y;
  const a = p.matrixTransform(matrix);

  p.x = r.x + r.width;
  p.y = r.y;
  const b = p.matrixTransform(matrix);

  p.x = r.x + r.width;
  p.y = r.y + r.height;
  const c = p.matrixTransform(matrix);

  p.x = r.x;
  p.y = r.y + r.height;
  const d = p.matrixTransform(matrix);

  const minX = Math.min(a.x, b.x, c.x, d.x);
  const maxX = Math.max(a.x, b.x, c.x, d.x);
  const minY = Math.min(a.y, b.y, c.y, d.y);
  const maxY = Math.max(a.y, b.y, c.y, d.y);

  const width = maxX - minX;
  const height = maxY - minY;

  return {
    x: minX,
    y: minY,
    width: width,
    height: height,
    cx: minX + width / 2,
    cy: minY + height / 2
  };
}