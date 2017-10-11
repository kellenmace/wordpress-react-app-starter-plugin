import React from 'react';
import { render } from 'react-dom';
import App from './components/App';
import './styles/style.scss';
require( './styles/style.scss' );

render( <App />, document.getElementById('app') );
