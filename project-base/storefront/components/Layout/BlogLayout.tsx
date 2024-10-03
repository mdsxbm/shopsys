import { Webline } from './Webline/Webline';
import { BlogSignpost } from 'components/Blocks/BlogSignpost/BlogSignpost';
import { useBlogCategories } from 'graphql/requests/blogCategories/queries/BlogCategoriesQuery.generated';
import { useRef } from 'react';

type BlogLayoutProps = {
    activeCategoryUuid: string;
};

export const BlogLayout: FC<BlogLayoutProps> = ({ children, activeCategoryUuid }) => {
    const paginationScrollTargetRef = useRef<HTMLDivElement>(null);
    const [{ data: blogCategoriesData }] = useBlogCategories();

    return (
        <Webline>
            <div className="scroll-mt-5" ref={paginationScrollTargetRef}>
                <div className="mb-16 flex flex-col vl:flex-row gap-10 xl:gap-[116px]">
                    <div className="order-2 flex flex-col w-full vl:order-1 vl:flex-1">{children}</div>
                    <div className="order-1 w-full vl:w-[300px] vl:order-2">
                        <BlogSignpost
                            activeItem={activeCategoryUuid}
                            blogCategoryItems={blogCategoriesData?.blogCategories}
                        />
                    </div>
                </div>
            </div>
        </Webline>
    );
};
