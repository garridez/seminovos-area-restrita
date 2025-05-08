import configs from 'snbh-site/eslint.config.mjs';
import tseslint from 'typescript-eslint';

export default tseslint.config(configs, {
    rules: {
        '@typescript-eslint/no-require-imports': 'warn',
        'prettier/prettier': 'off',
        '@typescript-eslint/no-this-alias': 'warn',
        '@typescript-eslint/no-unused-vars': 'warn',
        '@typescript-eslint/no-deprecated': 'warn',
    },
    languageOptions: {
        globals: {
            require: 'readonly',
            module: 'readonly',
        },
        parserOptions: {
            projectService: true,
            tsconfigRootDir: import.meta.dirname,
        },
    },
});
