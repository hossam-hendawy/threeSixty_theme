export function iOSVersion() {
  if (/iP(hone|od|ad)/.test(navigator.platform)) {
    // supports iOS 2.0 and later: <https://bit.ly/TJjs1V>
    const v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
    return parseInt(v[1], 10);
  }
  return false;
}
