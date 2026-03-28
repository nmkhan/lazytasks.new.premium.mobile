import licenseSlice from './licenseSlice'
import { combineReducers } from 'redux'
const reducer = combineReducers({
    license: licenseSlice,
})
export default reducer