const sleep = (time: number = 500) => {
    return new Promise(function (resolve) {
        setTimeout(() => {
            resolve(true);
        }, time);
    });
};

export default sleep;
