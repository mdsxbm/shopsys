import { SocialNetworkIcon } from './SocialNetworkLoginLinkIcon';
import { ExtendedNextLink } from 'components/Basic/ExtendedNextLink/ExtendedNextLink';
import { TypeLoginTypeEnum } from 'graphql/types';
import { twJoin } from 'tailwind-merge';
import { getSocialNetworkLoginLinkBg } from 'utils/getSocialNetworkLoginLinkBg';

export const SocialNetworkLoginLink: FC<{ href: string; socialNetwork: TypeLoginTypeEnum }> = ({
    href,
    socialNetwork,
}) => {
    const bgTwClasses = getSocialNetworkLoginLinkBg(socialNetwork);
    return (
        <ExtendedNextLink
            className={twJoin('size-14 flex justify-center items-center rounded-lg', bgTwClasses)}
            href={href}
        >
            <SocialNetworkIcon socialNetwork={socialNetwork} />
        </ExtendedNextLink>
    );
};
