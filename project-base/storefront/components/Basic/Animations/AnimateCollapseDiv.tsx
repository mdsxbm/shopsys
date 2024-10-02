import { TIDs } from 'cypress/tids';
import { HTMLMotionProps, m } from 'framer-motion';
import { collapseExpandAnimation } from 'utils/animations/animationVariants';

export const AnimateCollapseDiv: FC<HTMLMotionProps<'div'> & { tid?: TIDs; key?: string }> = ({
    children,
    className,
    key,
    tid,
    ...props
}) => (
    <m.div
        key={key}
        animate="open"
        className={className}
        exit="closed"
        initial="closed"
        tid={tid}
        variants={collapseExpandAnimation}
        {...props}
    >
        {children}
    </m.div>
);
