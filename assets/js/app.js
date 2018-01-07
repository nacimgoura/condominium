
global.$ = global.jQuery = require('jquery');
require('bootstrap/dist/js/bootstrap.bundle.min');
require('bootstrap/dist/css/bootstrap.min.css');
require('../fonts/iconfont/iconfont.css');
require('select2/dist/css/select2.min.css');
require('flatpickr/dist/flatpickr.min.css');
require('../css/style.css');
require('../css/own.css');
require('../css/forum.css');
import flatpickr from "flatpickr"
import { French } from "flatpickr/dist/l10n/fr";
flatpickr.localize(French);
require('select2');
require('../js/client');