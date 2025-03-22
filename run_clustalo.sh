#!/bin/bash

# 输入序列文件和输出文件
input_sequences="$1"
output_file="$2"

# 运行Clustal Omega进行序列比对
clustalo -i $input_sequences -o $output_file --force

echo "Clustal Omega序列比对已完成，结果保存在 $output_file"
