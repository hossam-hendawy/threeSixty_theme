  export function numberWithCommas(number) {
    return (+number).toFixed(+number%1===0?0:2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }