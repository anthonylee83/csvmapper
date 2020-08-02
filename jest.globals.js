global.Store = {
    gtag: () => Function
};

global.externalLibrary = {
    logError: err => {
        console.log(err); // will output errors during Jest run
    }
};
