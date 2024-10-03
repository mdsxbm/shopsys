import { ExtendedNextLink } from 'components/Basic/ExtendedNextLink/ExtendedNextLink';
import { Flag } from 'components/Basic/Flag/Flag';
import { Image } from 'components/Basic/Image/Image';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { TypeListedBlogArticleFragment } from 'graphql/requests/articlesInterface/blogArticles/fragments/ListedBlogArticleFragment.generated';
import { Fragment } from 'react';
import { twJoin } from 'tailwind-merge';

type BlogArticlesListProps = {
    blogArticles: TypeListedBlogArticleFragment[];
};

export const BlogArticlesList: FC<BlogArticlesListProps> = ({ blogArticles }) => {
    const { defaultLocale } = useDomainConfig();

    return (
        <ul className="flex w-full flex-col gap-y-5">
            {blogArticles.map((blogArticle) => (
                <li key={blogArticle.uuid} className="w-full">
                    <ExtendedNextLink
                        href={blogArticle.link}
                        type="blogArticle"
                        className={twJoin(
                            'flex w-full rounded-xl border border-backgroundMore flex-col p-4 md:flex-row md:gap-x-10 transition-colors',
                            'bg-backgroundMore no-underline',
                            'hover:bg-background hover:no-underline hover:border-borderAccentLess',
                        )}
                    >
                        <div className="mb-3 w-full text-center md:mb-0 md:w-48">
                            <Image
                                alt={blogArticle.mainImage?.name || blogArticle.name}
                                height={600}
                                sizes="(max-width: 600px) 100vw, 20vw"
                                src={blogArticle.mainImage?.url}
                                width={600}
                            />
                        </div>

                        <div className="flex flex-1 flex-col">
                            <div className="flex flex-wrap gap-x-6 gap-y-2 items-center">
                                <span className="text-[14px] leading-[18px] text-textSubtle font-secondary font-semibold">
                                    {new Date(blogArticle.publishDate).toLocaleDateString(defaultLocale)}
                                </span>
                                <div className="flex flex-wrap gap-2">
                                    {blogArticle.blogCategories.map((blogArticleCategory) => (
                                        <Fragment key={blogArticleCategory.uuid}>
                                            {blogArticleCategory.parent && (
                                                <Flag href={blogArticleCategory.link} type="blog">
                                                    {blogArticleCategory.name}
                                                </Flag>
                                            )}
                                        </Fragment>
                                    ))}
                                </div>
                            </div>

                            <h2 className="mb-3">{blogArticle.name}</h2>

                            {!!blogArticle.perex && <p className="mb-3 text-base">{blogArticle.perex}</p>}
                        </div>
                    </ExtendedNextLink>
                </li>
            ))}
        </ul>
    );
};
