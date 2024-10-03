import { BlogCategoryArticlesWrapper } from './BlogCategoryArticlesWrapper';
import { BlogCategoryHeader } from './BlogCategoryHeader';
import { BlogLayout } from 'components/Layout/BlogLayout';
import { TypeBlogCategoryDetailFragment } from 'graphql/requests/blogCategories/fragments/BlogCategoryDetailFragment.generated';
import { useRef } from 'react';

type BlogCategoryContentProps = {
    blogCategory: TypeBlogCategoryDetailFragment;
};

export const BlogCategoryContent: FC<BlogCategoryContentProps> = ({ blogCategory }) => {
    const paginationScrollTargetRef = useRef<HTMLDivElement>(null);

    return (
        <>
            <BlogCategoryHeader description={blogCategory.description} title={blogCategory.name} />
            <BlogLayout activeCategoryUuid={blogCategory.uuid}>
                <div className="order-2 mb-16 flex w-full flex-col vl:order-1 vl:flex-1">
                    <BlogCategoryArticlesWrapper
                        paginationScrollTargetRef={paginationScrollTargetRef}
                        uuid={blogCategory.uuid}
                    />
                </div>
            </BlogLayout>
        </>
    );
};
