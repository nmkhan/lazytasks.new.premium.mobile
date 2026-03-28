import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import {getLicenseKey, licenseDelete, licenseValidationCheck} from "../../../services/LicenseService";

// Fetch Single User
export const licenseValidation = createAsyncThunk(
    'license/licenseValidation',
    async (data) => {
    return licenseValidationCheck(data)
})

// Fetch Single User
export const licenseRemove = createAsyncThunk(
    'license/licenseRemove',
    async (data) => {
    return licenseDelete(data)
})

//fetch license key
export const fetchLicenseKey = createAsyncThunk(
    'license/fetchLicenseKey',
    async () => {
        return getLicenseKey()
    }
)


const initialState = {
    licenseKey:'',
    isLoading: false,
    isLicenseError: false,
    error: null,
    licenseSuccessMessage: null,
    isLicenseSuccess: false
}

const licenseSlice = createSlice({
    name: 'license',
    initialState,
    reducers: {
        removeSuccessMessage: (state) => {
            state.licenseSuccessMessage = null
        },
        removeErrorMessage: (state) => {
            state.error = ''
            state.isLicenseError = false
        }
    },
    extraReducers: (builder) => {
        builder
            .addCase(licenseValidation.pending, (state) => {
                state.isLoading = true
                state.isLicenseError = false
            })
            .addCase(licenseValidation.fulfilled, (state, action) => {
                state.isLoading = false
                state.isLicenseError = false
                state.licenseKey = action.payload.data && action.payload.data.license_key
                if (action.payload.data && action.payload.status === 200) {
                    state.licenseSuccessMessage = action.payload.message
                    state.isLicenseSuccess = true
                }
                if(action.payload.data && action.payload.status === 400) {
                    state.error = action.payload.message
                    state.isLicenseError = true
                }
            })
            .addCase(licenseValidation.rejected, (state, action) => {
                state.isLoading = false
                state.isLicenseError = false
                state.error = action.error?.message
            })
            .addCase(fetchLicenseKey.pending, (state) => {
                state.isLoading = true
                state.isLicenseError = false
            })
            .addCase(fetchLicenseKey.fulfilled, (state, action) => {
                state.isLoading = false
                state.isLicenseError = false
                state.licenseKey = action.payload.data && action.payload.data
                if (action.payload.data && action.payload.status === 200) {
                    state.isLicenseSuccess = true
                }
            })
            .addCase(fetchLicenseKey.rejected, (state, action) => {
                state.isLoading = false
                state.isLicenseError = false
                state.error = action.error?.message
            })
            .addCase(licenseRemove.pending, (state) => {
                state.isLoading = true
                state.isLicenseError = false
            })
            .addCase(licenseRemove.fulfilled, (state, action) => {
                state.licenseKey = ''
                state.isLicenseSuccess = false
            })
            .addCase(licenseRemove.rejected, (state, action) => {
                state.error = action.error?.message
            })

    },
})
export const {
    removeSuccessMessage,
    removeErrorMessage
} = licenseSlice.actions
export default licenseSlice.reducer
