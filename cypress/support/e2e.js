//commands
import "./helpers/user";
import "./helpers/data";
import "./helpers/subscription.js";
//localstorage
import "cypress-localstorage-commands";

import "@walmyr-filho/cy-press";

require('cypress-wait-until');

//for connection to db
const mongo = require("cypress-mongodb");
mongo.addCommands();
//xpath
require("cypress-xpath");

//скрыть xhr ответы
const app = window.top;
if (!app.document.head.querySelector("[data-hide-command-log-request]")) {
  const style = app.document.createElement("style");
  style.innerHTML =
    ".command-name-request, .command-name-xhr { display: none }";
  style.setAttribute("data-hide-command-log-request", "");

  app.document.head.appendChild(style);
}
