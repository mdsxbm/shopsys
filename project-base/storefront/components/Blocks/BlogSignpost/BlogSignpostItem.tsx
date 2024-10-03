import { ExtendedNextLink } from 'components/Basic/ExtendedNextLink/ExtendedNextLink';
import { twMergeCustom } from 'utils/twMerge';

type BlogSignpostItemProps = {
    isActive: boolean;
    href: string;
    itemLevel?: number;
};

export const BlogSignpostItem: FC<BlogSignpostItemProps> = ({ children, href, isActive, itemLevel }) => (
    <ExtendedNextLink
        href={href}
        style={itemLevel !== undefined ? { marginLeft: `calc(12px*${itemLevel})` } : {}}
        type="blogCategory"
        className={twMergeCustom(
            'transition-all relative flex gap-x-3 items-center text-[14px] leading-4 font-secondary py-[13px] px-3 font-semibold rounded-xl no-underline hover:no-underline bg-backgroundMore',
            isActive
                ? 'bg-backgroundAccent  text-textInverted no-underline hover:bg-backgroundAccentMore hover:text-textInverted'
                : 'text-text hover:text-text hover:bg-backgroundMost',
        )}
    >
        {children}
    </ExtendedNextLink>
);
