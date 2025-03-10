import React from 'react';

const AboutPage = () => {
    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif', lineHeight: '1.6' }}>
            <h1>About</h1>
            <p>
                Welcome to the Prompt Writing application! The vision of the creator is to create a platform that
                allows users to capture their thoughts and ideas in a simple and intuitive way.
            </p>
            <h2>Who I am</h2>
            <p>
                I am a passionate developer and thinker dedicated to building tools that inspire creativity
                and productivity. I believe that capturing your thoughts in the most intuitive way should be simple
                and accessible for everyone.
            </p>
            <h2>What I do</h2>
            <p>
                The platform is designed to:
            </p>
            <ul>
                <li>Help you create, tag, and organize notes effortlessly.</li>
                <li>Provide predefined prompts to inspire your writing and reflections.</li>
                <li>Enable custom categorization and tagging for easier organization.</li>
            </ul>
            <h2>Contact Us</h2>
            <p>
                Have questions or feedback? We'd love to hear from you!
                Reach out to us at <a href="mailto:example@example.example">example@example.example</a><br/>.
                <strong>- Note that it's just an example and no real contact information.</strong>
            </p>
        </div>
    );
};

export default AboutPage;