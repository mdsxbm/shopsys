import { TypeLoginTypeEnum } from 'graphql/types';

export const getSocialNetworkLoginLinkBg = (socialNetwork: TypeLoginTypeEnum) => {
    switch (socialNetwork) {
        case TypeLoginTypeEnum.Facebook:
            return 'bg-gradient-to-b from-[#19AFFF] to-[#0062E0]';
        case TypeLoginTypeEnum.Seznam:
            return 'bg-[#CC0000]';
        case TypeLoginTypeEnum.Google:
            return 'bg-[#FFFFFF] border-2 border-backgroundBrand';
        default:
            return '';
    }
};
