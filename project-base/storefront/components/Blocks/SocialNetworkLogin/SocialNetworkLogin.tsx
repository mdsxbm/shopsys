import { SocialNetworkLoginLink } from './SocialNetworkLoginLink';
import { TypeLoginTypeEnum } from 'graphql/types';
import React from 'react';
import { usePersistStore } from 'store/usePersistStore';

type SocialNetworkLoginProps = {
    socialNetworks: TypeLoginTypeEnum[];
    shouldOverwriteCustomerUserCart: boolean | undefined;
};

export const SocialNetworkLogin: FC<SocialNetworkLoginProps> = ({
    socialNetworks,
    shouldOverwriteCustomerUserCart,
}) => {
    const cartUuid = usePersistStore((store) => store.cartUuid);
    const productListUuids: string[] = Object.values(usePersistStore((store) => store.productListUuids));

    const getSocialNetworkLoginUrl = (
        socialNetwork: TypeLoginTypeEnum,
        cartUuid: string | null,
        shouldOverwriteCustomerUserCart: boolean | undefined,
        productListUuids: string[],
    ) => {
        let url = `/social-network/login/${socialNetwork}`;
        if (cartUuid) {
            url += `?cartUuid=${cartUuid}&shouldOverwriteCustomerUserCart=${shouldOverwriteCustomerUserCart ? 'true' : 'false'}`;
        }
        if (productListUuids.length > 0) {
            const separator = cartUuid ? '&' : '?';
            url += `${separator}productListUuids=${productListUuids.join(',')}`;
        }

        return url;
    };

    return (
        <div className="flex gap-4">
            {socialNetworks.map((socialNetwork) => (
                <SocialNetworkLoginLink
                    key={socialNetwork}
                    socialNetwork={socialNetwork}
                    href={getSocialNetworkLoginUrl(
                        socialNetwork,
                        cartUuid,
                        shouldOverwriteCustomerUserCart,
                        productListUuids,
                    )}
                />
            ))}
        </div>
    );
};
