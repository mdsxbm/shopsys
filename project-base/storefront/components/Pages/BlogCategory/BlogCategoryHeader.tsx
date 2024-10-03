import { Webline } from 'components/Layout/Webline/Webline';

type BlogCategoryHeaderProps = {
    title: string;
    description: string | null;
};

export const BlogCategoryHeader: FC<BlogCategoryHeaderProps> = ({ title, description }) => {
    return (
        <Webline className="xxl:max-w-[1432px] mb-10">
            <div className="bg-textAccent rounded-xl">
                <div className="px-5 py-[60px] xxl:max-w-7xl xxl:mx-auto xxl:px-4">
                    <h1 className="text-textInverted mb-3">{title}</h1>
                    <p className="text-textInverted">{description}</p>
                </div>
            </div>
        </Webline>
    );
};
