const webp = require('webp-converter');
const glob = require('glob');
const path = require('path');


//pass input image(.jpeg,.pnp .....) path ,output image(give path where to save and image file name with .webp extension)
//pass option(read  documentation for options)

//cwebp(input,output,option)
glob("old/*", {}, function (er, files) {
  // files is an array of filenames.
  // If the `nonull` option is set, and nothing
  // was found, then files is ["**/*.js"]
  // er is an error object or null.
  for (const file of files) {
    const fileName = path.parse(file).name;
    const result = webp.cwebp(file, `webp/${fileName}.webp`, "-q 80");
    result.then((response) => {
      console.log(fileName, '=> DONE');
    });
  }

})
