import { BlogSignpostIcon } from './BlogSignpostIcon';
import { BlogSignpostItem } from './BlogSignpostItem';
import { Children } from './Children';
import useTranslation from 'next-translate/useTranslation';
import { Fragment } from 'react';
import { ListedBlogCategoryRecursiveType } from 'types/blogCategory';

type BlogSingpostProps = {
    activeItem: string;
    blogCategoryItems?: ListedBlogCategoryRecursiveType[];
};

export const BlogSignpost: FC<BlogSingpostProps> = ({ blogCategoryItems, activeItem }) => {
    const { t } = useTranslation();

    return (
        <div className="flex flex-col gap-y-2.5">
            <p className="mb-0 font-secondary font-semibold">{t('Article categories')}</p>

            {blogCategoryItems?.map((blogCategory) => {
                const isActive = activeItem === blogCategory.uuid;

                return (
                    <Fragment key={blogCategory.uuid}>
                        <BlogSignpostItem href={blogCategory.link} isActive={isActive}>
                            <BlogSignpostIcon isActive={isActive} />
                            {blogCategory.name}
                        </BlogSignpostItem>
                        {!!blogCategory.children?.length && (
                            <Children activeItem={activeItem} blogCategory={blogCategory} itemLevel={1} />
                        )}
                    </Fragment>
                );
            })}
        </div>
    );
};
