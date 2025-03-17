/**
 * Config
 * -------------------------------------------------------------------------------------
 * ! IMPORTANT: Make sure you clear the browser local storage In order to see the config changes in the template.
 * ! To clear local storage: (https://www.leadshook.com/help/how-to-clear-local-storage-in-google-chrome-browser/).
 */

'use strict';

// JS global variables
let config = {
  colors: {
    primary: '#666cff',
    secondary: '#6d788d',
    success: '#72e128',
    info: '#26c6f9',
    warning: '#fdb528',
    danger: '#ff4d49',
    dark: '#4b4b4b',
    black: '#000',
    white: '#fff',
    cardColor: '#fff',
    bodyBg: '#f7f7f9',
    bodyColor: '#828393',
    headingColor: '#636578',
    textMuted: '#bbbcc4',
    borderColor: '#eaeaec'
  },
  colors_label: {
    primary: '#666cff29',
    secondary: '#6d788d29',
    success: '#72e12829',
    info: '#26c6f929',
    warning: '#fdb52829',
    danger: '#ff4d4929',
    dark: '#4b4b4b29'
  },
  colors_dark: {
    cardColor: '#30334e',
    bodyBg: '#282a42',
    bodyColor: '#a0a1b8',
    headingColor: '#d2d2e8',
    textMuted: '#777991',
    borderColor: '#464963'
  },
  enableMenuLocalStorage: true // Enable menu state with local storage support
};

let assetsPath = document.documentElement.getAttribute('data-assets-path'),
  baseUrl = document.documentElement.getAttribute('data-base-url') + '/',
  templateName = document.documentElement.getAttribute('data-template'),
  planlarRoutePath = document.documentElement.getAttribute('get-plan-route-path'),
  eanaceRoutePath = document.documentElement.getAttribute('get-eanace-route-path'),
  cat22RoutePath = document.documentElement.getAttribute('get-cat22-route-path'),
  catSmiicRoutePath = document.documentElement.getAttribute('get-catsmiic-route-path'),
  cat27001RoutePath = document.documentElement.getAttribute('get-cat27001-route-path'),
  cat50001RoutePath = document.documentElement.getAttribute('get-cat50001-route-path'),
  getDenetimOnerilenBasdenetciPath = document.documentElement.getAttribute('get-denetim-onerilen-basdenetci-path'),
  getOnerilenKararUyeleriPath = document.documentElement.getAttribute('get-onerilen-karar-uyeleri-path'),
  dtBasdenetciRoutePath = document.documentElement.getAttribute('get-basdenetci-path'),
  dtDenetciRoutePath = document.documentElement.getAttribute('get-denetci-path'),
  dtTeknikUzmanRoutePath = document.documentElement.getAttribute('get-teknik-uzman-path'),
  dtGozlemciRoutePath = document.documentElement.getAttribute('get-gozlemci-path'),
  dtIkuRoutePath = document.documentElement.getAttribute('get-iku-path'),
  dtAdayDenetciRoutePath = document.documentElement.getAttribute('get-aday-denetci-path'),
  denetimTakvimiRoutePath = document.documentElement.getAttribute('denetim-takvimi-path'),
  tblbelgelifirmalarals05RoutePath = document.documentElement.getAttribute('tblbelgelifirmalarals05-path'),
  rtlSupport = true; // set true for rtl support (rtl + ltr), false for ltr only.
