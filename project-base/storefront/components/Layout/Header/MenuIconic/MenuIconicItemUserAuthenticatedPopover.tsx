import { AnimateAppearDiv } from 'components/Basic/Animations/AnimateAppearDiv';
import { AnimatePresence } from 'framer-motion';
import { twMergeCustom } from 'utils/twMerge';

type MenuIconicItemUserAuthenticatedPopoverProps = {
    isHovered: boolean;
};

export const MenuIconicItemUserAuthenticatedPopover: FC<MenuIconicItemUserAuthenticatedPopoverProps> = ({
    isHovered,
    children,
}) => {
    return (
        <AnimatePresence initial={false}>
            {isHovered && (
                <AnimateAppearDiv
                    className={twMergeCustom(
                        'p-5 pointer-events-auto absolute top-0 -right-[100%] hidden lg:block min-w-[355px] origin-top rounded-xl',
                        'top-full bg-background z-cart',
                    )}
                >
                    {children}
                </AnimateAppearDiv>
            )}
        </AnimatePresence>
    );
};
