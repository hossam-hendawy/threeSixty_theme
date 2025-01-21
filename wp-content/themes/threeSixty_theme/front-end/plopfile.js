const crypto = require('crypto')
const userName = require('git-user-name');
module.exports = function (plop) {

  plop.addHelper("upperCase", text => text.toUpperCase());
  const pascalCase = (s) =>
    s.replace(/\w+/g, function (w) {
      return w[0].toUpperCase() + w.slice(1);
    });
  const files = {
    frontEnd: {
      js: './plop-templates/front-end-templates/index.js',
      style: './plop-templates/front-end-templates/style.scss',
    },
    backEnd: {
      php: './plop-templates/back-end-templates/index.php',
      ACFJson: './plop-templates/back-end-templates/acf-json-template.json.hbs',

    },
  };

  const createJs = {
    type: "add",
    path: "./src/blocks/{{snakeCase name}}/index.js",
    templateFile: files.frontEnd.js,
  };

  const createStyle = {
    type: "add",
    path: "./src/blocks/{{snakeCase name}}/style.scss",
    templateFile: files.frontEnd.style,
  };

  const createPhp = {
    type: "add",
    path: "../blocks/{{snakeCase name}}/index.php",
    templateFile: files.backEnd.php,
  };

  const createACFJson = {
    type: "add",
    path: "../acf-json/group_{{hash}}.json",
    templateFile: files.backEnd.ACFJson,
  };

  const createFrontEndBlockFiles = {
    type: "addMany",
    destination: "./src/blocks/{{snakeCase name}}",
    base: `plop-templates/front-end-templates`,
    templateFiles: `plop-templates/front-end-templates/*.hbs`
  }

  const createStyledComp = {
    type: "append",
    pattern: '.*(?=\/\* -- blocks will be registered above -- \*\/)',
    path: "../blocks/{{snakeCase name}}/index.php",
    templateFile: files.backEnd.php,
    data: {name: '', domain: 'threeSixty_theme'}
  };


  /* Input Options */
  const getBlockName = {
    type: "input",
    name: "name",
    message: "What is the block name?",
    validate: function (value) {
      if (/.+/.test(value)) {
        return true;
      }
      return "name is required";
    },
  };
  const getName = {
    type: "input",
    name: "user",
    message: "",
    waitUserInput: false,
    default: () => userName()
  };

  const getTime = {
    type: "input",
    name: "time",
    message: "",
    waitUserInput: false,
    default: () => ~~(new Date().getTime() / 1000)
  };
  const getHash = {
    type: "input",
    name: "hash",
    message: "",
    waitUserInput: false,
    default: () => crypto.randomBytes(7).toString("hex")
  };
  const getNewBlockName = {
    type: "input",
    name: "newName",
    message: "What is the new block name?",
    validate: function (value) {
      if (/.+/.test(value)) {
        return true;
      }
      return "name is required";
    },
  };

  plop.setHelper('addHash', (txt) => txt.slice(0, -4) + crypto.randomBytes(2).toString("hex"));

  /* Generators */
  plop.setGenerator("cb", {
    description: "Create Block",
    prompts: [getBlockName, getTime, getHash, getName],
    actions: [
      createFrontEndBlockFiles,
      createPhp,
      createACFJson,
    ],
  });

  // plop.setGenerator("eb", {
  //   description: "Edit Block Name",
  //   prompts: [getOldBlockName, getNewBlockName],
  //   actions: [
  //     createStyledComp,
  //   ]
  // });
};
