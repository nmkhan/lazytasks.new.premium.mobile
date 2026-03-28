import React, {useState} from 'react';
import { NavLink } from '@mantine/core';


const LicenseNav = () => {
    const [active, setActive] = useState(false);
    const location = window?.location?.hash;

    return (
        <>
            <NavLink
                href="#/license"
                key={`License`}
                active={active}
                label={`License`}
                onClick={() => setActive(true)}
                variant="filled"
                size="sm"
                color={location==='#/license' ? "#fff" : "#000"}
                className={`rounded font-semibold text-black-600 bg:hover`}
                style={{ backgroundColor: location==='#/license' ? "#39758D" : "#EBF1F4",  color: location==='#/license' ? "#fff" : "#000"}}
            />
        </>
        
    );
}

export default LicenseNav;
