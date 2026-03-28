// src/index.js
import { render } from '@wordpress/element';
import App from "./App";

if (document.getElementById('lazytasks-premium')) {
    render( <App />, document.getElementById('lazytasks-premium'));
}