import { combineReducers } from 'redux'

// import PremiumBaseReducer from './base';
import qrCodeReducer from '../components/qrCode/store';
import licenseReducer from '../components/license/store';

const premiumRootReducer = combineReducers({
    qrcode: qrCodeReducer,
    license: licenseReducer,
});

export default premiumRootReducer
